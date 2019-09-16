using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Data.Entity;
using CustomerKeys.DAL.Entities;

namespace CustomerKeys.DAL.EF
{
    public class DataContext : DbContext
    {
        public DbSet<Customer> Customers { get; set; }

        public DbSet<Key> Keys { get; set; }

        public DataContext(string connectionString)
            : base(connectionString)
        {
        }
    }
}