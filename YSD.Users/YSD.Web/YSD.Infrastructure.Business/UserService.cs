using System.Collections.Generic;
using YSD.Domain.Core;
using YSD.Infrastructure.Interfaces;
using YSD.Services.Interfaces;

namespace YSD.Infrastructure.Business
{
    public class UserService : IUserService
    {
        IUnitOfWork Database { get; set; }

        public UserService(IUnitOfWork uow)
        {
            Database = uow;
        }

        public IEnumerable<User> GetUsers()
        {
            return Database.Users.GetAll();
        }

        public void Dispose()
        {
            Database.Dispose();
        }
    }
}
