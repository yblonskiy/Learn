using CustomerKeys.DAL.EF;
using CustomerKeys.DAL.Entities;
using CustomerKeys.DAL.Interfaces;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Data.Entity;

namespace CustomerKeys.DAL.Repositories
{
    public class KeyRepository : IRepository<Key>
    {
        private DataContext db;

        public KeyRepository(DataContext context)
        {
            this.db = context;
        }

        public Key Get(int id)
        {
            return db.Keys.Find(id);
        }

        public IEnumerable<Key> GetAll()
        {
            return db.Keys;
        }

        public IEnumerable<Key> Find(Func<Key, bool> predicate)
        {
            return db.Keys.Where(predicate).ToList();
        }

        public Key Create(Key item)
        {
            return db.Keys.Add(item);
        }

        public void Delete(int id)
        {
            Key key = db.Keys.Find(id);

            if (key != null)
            {
                db.Keys.Remove(key);
            }
        }

        public void Update(Key item)
        {
            db.Entry(item).State = EntityState.Modified;
        }
    }
}
