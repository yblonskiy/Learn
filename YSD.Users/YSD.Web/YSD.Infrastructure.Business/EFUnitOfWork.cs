using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using YSD.Infrastructure.Data;
using YSD.Domain.Interfaces;
using YSD.Domain.Core;
using YSD.Infrastructure.Interfaces;

namespace YSD.Infrastructure.Business
{
    public class EFUnitOfWork : IUnitOfWork
    {
        private UserContext db;
        private UserRepository userRepository;

        private bool disposed = false;

        public EFUnitOfWork(string connectionString)
        {
            db = new UserContext(connectionString);
        }

        public IRepository<User> Users
        {
            get
            {
                if (userRepository == null)
                {
                    userRepository = new UserRepository(db);
                }

                return userRepository;
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
                    db = null;
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
