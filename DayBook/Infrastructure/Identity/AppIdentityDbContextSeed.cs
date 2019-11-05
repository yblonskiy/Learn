using Microsoft.AspNetCore.Identity;
using System.Threading.Tasks;

namespace Infrastructure.Identity
{
    public class AppIdentityDbContextSeed
    {
        public static async Task SeedAsync(UserManager<ApplicationUser> userManager)
        {
            var defaultUser = new ApplicationUser { UserName = "Admin", Email = "admin@gmail.com" };
            await userManager.CreateAsync(defaultUser, "c4ca4238a0b923820dcc509a6f75849b"); // pwd 1
        }
    }
}
