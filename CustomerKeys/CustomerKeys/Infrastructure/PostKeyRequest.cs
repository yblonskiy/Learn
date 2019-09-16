using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using CustomerKeys.Models;

namespace CustomerKeys.Infrastructure
{
    public class PostKeyRequest
    {
        public Customer Customer { get; set; }

        public ushort Numbers { get; set; }
    }
}