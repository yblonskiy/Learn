using DayBook.Application.Auth;
using DayBook.Application.Interfaces;
using DayBook.Web.ViewModels.Account;
using Infrastructure.Identity;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Caching.Distributed;
using Microsoft.Extensions.Options;
using System.Threading.Tasks;

namespace DayBook.Web.Controllers
{
    public class AccountController : Controller
    {
        private readonly IAccountService _accountService;
        private readonly JwtIssuerOptions _jwtOptions;

        public AccountController(IAccountService accountService,
            IOptions<JwtIssuerOptions> jwtOptions)
        {
            _accountService = accountService;
            _jwtOptions = jwtOptions.Value;
        }

        [HttpGet]
        [AllowAnonymous]
        public ActionResult Login(string returnUrl = "") => View(new LoginViewModel { ReturnUrl = returnUrl });

        //
        // POST: /Account/Login
        [HttpPost]
        [AllowAnonymous]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Login(LoginViewModel model)
        {
            if (!ModelState.IsValid)
            {
                return View(model);
            }

            var user = await _accountService.GetVerifiedUserAsync(model.Email, model.Password);

            if (user == null)
            {
                return NotFound();
            }

            if (await _accountService.IsMarkedAsDeletedAsync(model.Email))
            {
                return RedirectToAction(nameof(Reopen), new { email = model.Email });
            }

            HttpContext.Session.SetString("JWToken", await _accountService.GenerateToken(user));

            if (!string.IsNullOrEmpty(model.ReturnUrl) && Url.IsLocalUrl(model.ReturnUrl))
            {
                return Redirect(model.ReturnUrl);
            }
            else
            {
                return RedirectToAction("Index", "Record");
            }




            //if (await _accountService.IsMarkedAsDeletedAsync(model.Email))
            //{
            //    return RedirectToAction(nameof(Reopen), new { email = model.Email });
            //}

            //if (!await _accountService.LoginUserAsync(model.Email, model.Password))
            //{
            //    ModelState.AddModelError(string.Empty, "Wrong username or password");
            //    return View(model);
            //}

            //HttpContext.Session.SetString("email", model.Email);

            //if (!string.IsNullOrEmpty(model.ReturnUrl) && Url.IsLocalUrl(model.ReturnUrl))
            //{
            //    return Redirect(model.ReturnUrl);
            //}
            //else
            //{
            //    return RedirectToAction("Index", "Record");
            //}
        }

        // GET: /Account/Register
        [HttpGet]
        [AllowAnonymous]
        [Route("[controller]/[action]/{code}")]
        public async Task<ActionResult> Register(string code)
        {
            if (string.IsNullOrEmpty(code))
            {
                return BadRequest();
            }

            if (!await _accountService.IsExistInviteAsync(code))
            {
                return NotFound();
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
                return View(model);
            }

            var result = await _accountService.RegisterUserAsync(new ApplicationUser
            {
                UserName = model.Email,
                NickName = model.NickName,
                Email = model.Email
            }, model.Password, model.Code);

            if (!result.Success)
            {
                ModelState.AddModelError(string.Empty, result.Message);
                return View(model);
            }

            HttpContext.Session.SetString("email", model.Email);

            return RedirectToAction("Index", "Record");
        }

        [HttpPost]
        public async Task<IActionResult> Logout()
        {
            HttpContext.Session.Clear();
            //await _accountService.LogoutUserAsync();

            return RedirectToAction("Login", "Account");
        }

        [HttpGet]
        [AllowAnonymous]
        public async Task<ActionResult> Reopen(string email)
        {
            var user = await _accountService.GetReopenUserAsync(email);

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
                ModelState.AddModelError(string.Empty, "Email not found.");
                return View(model);
            }

            var result = await _accountService.ReopenAccountAsync(model.Email);

            if (!result.Success)
            {
                ModelState.AddModelError(string.Empty, result.Message);
                return View(model);
            }

            HttpContext.Session.SetString("email", model.Email);

            return RedirectToAction("Index", "Record");
        }

        [HttpGet]
        [AllowAnonymous]
        public ActionResult AccessDenied()
        {
            return View();
        }
    }
}