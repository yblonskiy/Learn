using System;
using System.Collections.Generic;
using System.Linq;
using System.Web.Http;
using CustomerKeys.BLL.Interfaces;
using CustomerKeys.BLL.DTO;
using CustomerKeys.WEB.Models;
using AutoMapper;

namespace CustomerKeys.WEB.Controllers
{
    public class KeyController : ApiController
    {
        IKeyService keyService;

        public KeyController(IKeyService service)
        {
            this.keyService = service;
        }

        /// <summary>
        /// GET: api/key
        /// Returns list of customers
        /// </summary>
        /// <returns>list of customers</returns>
        public IEnumerable<CustomerDTO> GetCustomers()
        {
            return keyService.GetCustomers().OrderByDescending(c => c.Id).ToList();
        }

        /// <summary>
        /// GET: api/key/XXX, where XXX is integer
        /// Returns list of keys by customer Id
        /// </summary>
        /// <param name="id">customer Id</param>
        /// <returns>list of keys</returns>
        public IEnumerable<KeyDTO> GetKeys(int id)
        {
            return keyService.GetKeys(id);
        }

        /// <summary>
        /// POST: api/key
        /// Creates a new customer (if doesn't exist) and generates key(s) with save to database
        /// </summary>
        /// <param name="postKeyRequest"></param>
        /// <returns></returns>
        [HttpPost]
        public IHttpActionResult GenerateKey([FromBody]KeyRequestModel postKeyRequest)
        {
            if (postKeyRequest.Customer == null || postKeyRequest.Numbers == 0)
            {
                return BadRequest();
            }
          
            if (keyService.GenerateKey(postKeyRequest.Customer, postKeyRequest.Numbers))
            {
                return Ok();
            }
            else
            {
                return BadRequest();
            }
        }
      
        protected override void Dispose(bool disposing)
        {
            base.Dispose(disposing);
        }
    }
}
