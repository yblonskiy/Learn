using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data.Entity;
using YSD.Domain.Interfaces;
using YSD.Domain.Core;

namespace YSD.Infrastructure.Data
{
    public class UserRepository : IRepository<User>
    {
        private UserContext db;

        public UserRepository(UserContext context)
        {
            this.db = context;
        }
                
        public IEnumerable<User> GetAll()
        {
            return db.Users;
        }


        public User Get(int id)
        {
            return db.Users.Find(id);
        }

        public IEnumerable<User> Find(Func<User, bool> predicate)
        {
            return db.Users.Where(predicate).ToList();
        }

        public User Create(User item)
        {
            return db.Users.Add(item);
        }

        public void Delete(int id)
        {
            User key = db.Users.Find(id);

            if (key != null)
            {
                db.Users.Remove(key);
            }
        }

        public void Update(User item)
        {
            db.Entry(item).State = EntityState.Modified;
        }
    }
}
