using System;
using CustomerKeys.DAL.EF;
using CustomerKeys.DAL.Entities;
using CustomerKeys.DAL.Interfaces;

namespace CustomerKeys.DAL.Repositories
{
    public class EFUnitOfWork : IUnitOfWork
    {
        private DataContext db;
        private KeyRepository keyRepository;
        private CustomerRepository customerRepository;

        private bool disposed = false;

        public EFUnitOfWork(string connectionString)
        {
            db = new DataContext(connectionString);
        }

        public IRepository<Customer> Customers
        {
            get {
                if (customerRepository == null)
                {
                    customerRepository = new CustomerRepository(db);
                }

                return customerRepository;
            }
        }

        public IRepository<Key> Keys {
            get {
                if (keyRepository == null)
                {
                    keyRepository = new KeyRepository(db);
                }

                return keyRepository;
            }
        }

        public bool Save()
        {
            return db.SaveChanges() > 0;
        }

        public virtual void Dispose(bool disposing)
        {
            if (!this.disposed)
            {
                if (disposing)
                {
                    db.Dispose();
                }

                this.disposed = true;
            }
        }

        public void Dispose()
        {
            Dispose(true);
            GC.SuppressFinalize(this);
        }

    }
}
