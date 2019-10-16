using System.Collections.Generic;
using System.Linq;
using System.Web.Mvc;
using YSD.Domain.Core;
using YSD.Services.Interfaces;
using YSD.Web.Models;

namespace YSD.Web.Controllers
{
    public class HomeController : Controller
    {
        IUserService userService;

        /// <summary>
        /// Uses Ninject IoC to inject dependency
        /// </summary>
        /// <param name="userService">IUserService</param>
        public HomeController(IUserService userService)
        {
            this.userService = userService;
        }

        public ActionResult Index()
        {
            // Checks if user logged
            if (!User.Identity.IsAuthenticated)
            {
                return RedirectToAction("Login", "Account");
            }

            // Gets users from service
            IEnumerable<User> featuredUsers =
            this.userService.GetUsers();

            // Wraps each user in view model
            var vm = new FeaturedUsersViewModel(
                from user in featuredUsers
                select new UserViewModel(user));

            return this.View(vm);
        }

        public ViewResult About()
        {
            return this.View();
        }
    }
}