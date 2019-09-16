using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Data.Entity;

namespace CustomerKeys.Models
{
    public class KeyContext : DbContext
    {
        public DbSet<Customer> Customers { get; set; }

        public DbSet<Key> Keys { get; set; }
    }
}