using DayBook.Application.Interfaces;
using DayBook.Web.ViewModels.Account;
using Infrastructure.Identity;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Caching.Distributed;
using System.Threading.Tasks;

namespace DayBook.Web.Controllers
{
    public class AccountController : Controller
    {
        private readonly IAccountService _accountService;

        public AccountController(IAccountService accountService)
        {
            _accountService = accountService;
        }

        [HttpGet]
        [AllowAnonymous]
        public ActionResult Login(string returnUrl = "")
        {
            var model = new LoginViewModel { ReturnUrl = returnUrl };
            return View(model);
        }

        //
        // POST: /Account/Login
        [HttpPost]
        [AllowAnonymous]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Login(LoginViewModel model)
        {
            if (!ModelState.IsValid)
            {
                return BadRequest(ModelState);
            }

            if (await _accountService.IsMarkedAsDeletedAsync(model.Email))
            {
                return RedirectToAction(nameof(Reopen), new { email = model.Email });
            }

            if (!await _accountService.LoginUserAsync(model.Email, model.Password))
            {
                ModelState.AddModelError(string.Empty, "Wrong username or password");
                return View(model);
            }

            HttpContext.Session.SetString("email", model.Email);

            if (!string.IsNullOrEmpty(model.ReturnUrl) && Url.IsLocalUrl(model.ReturnUrl))
            {
                return Redirect(model.ReturnUrl);
            }
            else
            {
                return RedirectToAction("Index", "Record");
            }
        }

        // GET: /Account/Register
        [HttpGet]
        [AllowAnonymous]
        [Route("[controller]/[action]")]
        public ActionResult Register(string code)
        {
            if (string.IsNullOrEmpty(code))
            {
                return RedirectToAction("Login", "Account");
            }

            var model = new RegisterViewModel
            {
                Code = code
            };

            return View(model);
        }

        // POST: /Account/Register
        [HttpPost]
        [AllowAnonymous]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Register(RegisterViewModel model)
        {
            if (!ModelState.IsValid)
            {
                string errorMsg = null;

                foreach (var test in ModelState.Values)
                {
                    foreach (var msg in test.Errors)
                    {
                        errorMsg = msg.ErrorMessage;
                    }
                }
                return BadRequest(errorMsg);
            }

            if (await _accountService.RegisterUserAsync(new ApplicationUser
            {
                UserName = model.Email,
                NickName = model.NickName,
                Email = model.Email
            }, model.Password, model.Code))
            {
                HttpContext.Session.SetString("email", model.Email);

                return RedirectToAction("Index", "Record");
            }
            else
            {
                return new BadRequestObjectResult("Error of register a new user.");
            }
        }

        [HttpPost]
        public async Task<IActionResult> Logout()
        {
            await _accountService.LogoutUserAsync();

            return RedirectToAction("Login", "Account");
        }

        [HttpGet]
        [AllowAnonymous]
        public async Task<ActionResult> Reopen(string email)
        {
            var user = await _accountService.IsPossibleReopenAccountAsync(email);
            if (user == null)
            {
                return NotFound();
            }

            return View(new ReopenViewModel { Id = user.Id, Email = user.Email });
        }

        [HttpPost]
        [AllowAnonymous]
        public async Task<ActionResult> Reopen(ReopenViewModel model)
        {
            if (model.Email == null)
            {
                return NotFound();
            }

            if (await _accountService.ReopenAccountAsync(model.Email))
            {
                HttpContext.Session.SetString("email", model.Email);

                return RedirectToAction("Index", "Record");
            }
            else
            {
                return new BadRequestObjectResult("Error of re-open a account.");
            }
        }

        [HttpGet]
        [AllowAnonymous]
        public ActionResult AccessDenied()
        {
            return View();
        }
    }
}