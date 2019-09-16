using CustomerKeys.Infrastructure;
using CustomerKeys.Models;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Security.Cryptography;
using System.Web.Http;
using System.Net.Http;

namespace CustomerKeys.Controllers
{
    public class CustomerController : ApiController
    {
        KeyContext db = new KeyContext();

        /// <summary>
        /// GET: api/customer
        /// Returns list of customers
        /// </summary>
        /// <returns>list of customers</returns>
        public IEnumerable<Customer> GetCustomers()
        {
            return db.Customers.OrderByDescending(c => c.Id).ToList();
        }

        /// <summary>
        /// POST: api/customer
        /// Creates a new customer (if doesn't exist) and generates key(s) with save to database
        /// </summary>
        /// <param name="postKeyRequest"></param>
        /// <returns></returns>
        [HttpPost]
        public IHttpActionResult GenerateKey([FromBody]PostKeyRequest postKeyRequest)
        {
            if (postKeyRequest.Customer == null || postKeyRequest.Numbers == 0)
            {
                return BadRequest();
            }

            // Searches the customer by Name in database
            Customer custm = db.Customers.FirstOrDefault(p => p.Name.Equals(postKeyRequest.Customer.Name, StringComparison.OrdinalIgnoreCase));

            if (custm == null)
            {
                // Adds a new customer to database
                custm = db.Customers.Add(new Customer { Name = postKeyRequest.Customer.Name });

                // Saves adding a new customer to database
                db.SaveChanges();
            }

            for (int i = 0; i < postKeyRequest.Numbers; i++)
            {
                // Generates a key
                string key = Helper.GetKey();
                bool isFound = true;

                while (isFound)
                {
                    // Checks if exist key in database
                    isFound = Helper.IsExistsKey(key, db);

                    // If exists the key then generates a new unique key
                    if (isFound)
                    {
                        key = Helper.GetKey();
                    }
                }

                // Adds the key to database
                db.Keys.Add(new Key { CustomerId = custm.Id, Value = key });
            }

            // Saves all changes to database
            db.SaveChanges();

            return Ok();
        }              

        protected override void Dispose(bool disposing)
        {
            if (disposing)
            {
                db.Dispose();
            }

            base.Dispose(disposing);
        }
    }
}
