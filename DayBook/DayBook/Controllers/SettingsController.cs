using DayBook.Application.Interfaces;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using System.Threading.Tasks;

namespace DayBook.Web.Controllers
{
    [Authorize(Roles = "User")]
    public class SettingsController : Controller
    {
        private readonly IAccountService _accountService;

        public SettingsController(IAccountService accountService)
        {
            _accountService = accountService;
        }

        public IActionResult Index() => View();

        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteAccount()
        {
            var result = await _accountService.MarkAsDeletedAsync(User.Identity.Name);

            if (!result.Success)
            {
                ModelState.AddModelError(string.Empty, result.Message);
                return View();
            }

            await _accountService.LogoutUserAsync();

            return RedirectToAction("Login", "Account");
        }
    }
}