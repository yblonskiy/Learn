using System.Data.Entity;
using YSD.Domain.Core;

namespace YSD.Infrastructure.Data
{
    public class UserContext : DbContext
    {
        public DbSet<User> Users { get; set; }

        public UserContext(string connectionString)
         : base(connectionString)
        {
        }
    }
}
