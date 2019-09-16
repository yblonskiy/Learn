using CustomerKeys.DAL.EF;
using CustomerKeys.DAL.Entities;
using CustomerKeys.DAL.Interfaces;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Data.Entity;

namespace CustomerKeys.DAL.Repositories
{
    public class CustomerRepository : IRepository<Customer>
    {
        private DataContext db;

        public CustomerRepository(DataContext context)
        {
            this.db = context;
        }

        public Customer Get(int id)
        {
            return db.Customers.Find(id);
        }

        public IEnumerable<Customer> GetAll()
        {
            return db.Customers;
        }

        public IEnumerable<Customer> Find(Func<Customer, bool> predicate)
        {
            return db.Customers.Where(predicate).ToList();
        }

        public Customer Create(Customer item)
        {
            return db.Customers.Add(item);
        }

        public void Delete(int id)
        {
            Customer customer = db.Customers.Find(id);

            if (customer != null)
            {
                db.Customers.Remove(customer);
            }
        }

        public void Update(Customer item)
        {
            db.Entry(item).State = EntityState.Modified;
        }
    }
}
