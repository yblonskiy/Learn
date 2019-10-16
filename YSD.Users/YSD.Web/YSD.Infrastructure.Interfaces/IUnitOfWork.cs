using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using YSD.Domain.Core;
using YSD.Domain.Interfaces;

namespace YSD.Infrastructure.Interfaces
{
    public interface IUnitOfWork : IDisposable
    {
        IRepository<User> Users { get; }
        
        bool Save();
    }
}
