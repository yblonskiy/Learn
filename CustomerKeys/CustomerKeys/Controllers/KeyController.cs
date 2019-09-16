using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Web.Http;
using CustomerKeys.Models;

namespace CustomerKeys.Controllers
{
    public class KeyController : ApiController
    {
        KeyContext db = new KeyContext();

        /// <summary>
        /// GET: api/key/XXX, where XXX is integer
        /// Returns list of keys by customer Id
        /// </summary>
        /// <param name="id">customer Id</param>
        /// <returns>list of keys</returns>
        public IEnumerable<Key> GetKeys(int id)
        {
            return db.Keys.Where(k => k.CustomerId == id).ToList();
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
