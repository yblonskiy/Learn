using System;
using System.Collections.Generic;
using System.Linq;
using YSD.Domain.Core;
using YSD.Infrastructure.Interfaces;
using YSD.Services.Interfaces;

namespace YSD.Services.Business
{
    public class UserService : IUserService
    {
        IUnitOfWork Database { get; set; }

        public UserService(IUnitOfWork uow)
        {
            Database = uow;
        }

        /// <summary>
        /// Checks if exists a user
        /// </summary>
        /// <param name="email">Email</param>
        /// <param name="password">Password, hashed MD5</param>
        /// <returns>If user is logged returns true, else returns false</returns>
        public bool Login(string email, string password)
        {
            Func<User, bool> FindUser = delegate (User u)
            {
                return u.Email.Equals(email, StringComparison.OrdinalIgnoreCase)
                    && u.Password.Equals(password, StringComparison.OrdinalIgnoreCase);
            };

            return Database.Users.Find(FindUser).ToList().Count > 0;
        }

        /// <summary>
        /// Creates a new user in database
        /// </summary>
        /// <param name="user">User</param>
        /// <returns>If user is created returns true, else returns false</returns>
        public bool Register(User user)
        {
            Func<User, bool> FindUser = delegate (User u)
            {
                return u.Email.Equals(user.Email, StringComparison.OrdinalIgnoreCase);
            };

            // Checks if user exists in database
            if (Database.Users.Find(FindUser).ToList().Count > 0)
            {
                return false;
            }

            Database.Users.Create(user);
      
            return Database.Save();
        }

        /// <summary>
        /// Returns list of all users
        /// </summary>
        /// <returns></returns>
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
