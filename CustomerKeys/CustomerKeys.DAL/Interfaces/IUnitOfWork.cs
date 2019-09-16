using CustomerKeys.DAL.Entities;
using System;

namespace CustomerKeys.DAL.Interfaces
{
    public interface IUnitOfWork : IDisposable
    {
        IRepository<Customer> Customers { get; }

        IRepository<Key> Keys { get; }

        bool Save();
    }
}
