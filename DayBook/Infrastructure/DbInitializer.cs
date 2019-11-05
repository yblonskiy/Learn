using Infrastructure.Identity;
using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.DependencyInjection;
using System;
using System.Linq;
using System.Threading.Tasks;

namespace Infrastructure
{
    public class DbInitializer
    {
        private AppIdentityDbContext _context;
        private UserManager<ApplicationUser> _userManager;
        private RoleManager<ApplicationRole> _roleManager;

        public DbInitializer(AppIdentityDbContext context,
                             UserManager<ApplicationUser> userManager,
                             RoleManager<ApplicationRole> roleManager)
        {
            _context = context;
            _userManager = userManager;
            _roleManager = roleManager;
        }

        public async Task InitializeData()
        {
            if (_context.Users.Any())
            {
                return;
            }

            ////////// Create Two Roles (Admin, User) and one admin account assigned with proper roles //////////

            var findAdminRole = await _roleManager.FindByNameAsync("Admin");
            var findUserRole = await _roleManager.FindByNameAsync("User");
            var adminRole = new ApplicationRole("Admin");
            var userRole = new ApplicationRole("User");

            //If admin role does not exists, create it
            if (findAdminRole == null)
            {
                await _roleManager.CreateAsync(adminRole);
            }

            //If user role does not exists, create it
            if (findUserRole == null)
            {
                await _roleManager.CreateAsync(userRole);
            }

            var findAdminAccount = await _userManager.FindByNameAsync("admin@gmail.com");

            //If there is no user account "admin", create it       
            if (findAdminAccount == null)
            {
                var admin = new ApplicationUser()
                {
                    UserName = "admin@gmail.com",
                    NickName = "Admin",
                    Email = "admin@gmail.com",
                    SecurityStamp = Guid.NewGuid().ToString()
                };

                var result = await _userManager.CreateAsync(admin, "P@$$w0rd");               
            }

            var adminAccount = await _userManager.FindByNameAsync("admin@gmail.com");

            //If Admin account is not in an admin role, add it to the role.
            if (!await _userManager.IsInRoleAsync(adminAccount, adminRole.Name))
            {
                await _userManager.AddToRoleAsync(adminAccount, adminRole.Name);
            }
        }
    }
}
