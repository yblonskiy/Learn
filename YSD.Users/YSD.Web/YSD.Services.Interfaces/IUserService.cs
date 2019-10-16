using System.Collections.Generic;
using YSD.Domain.Core;

namespace YSD.Services.Interfaces
{
    public interface IUserService
    {
        IEnumerable<User> GetUsers();

        bool Login(string email, string password);

        bool Register(User user);

        void Dispose();
    }
}
