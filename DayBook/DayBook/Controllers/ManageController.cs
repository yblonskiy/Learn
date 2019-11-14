using ApplicationCore.Entities;
using ApplicationCore.Interfaces;
using DayBook.Application.Interfaces;
using DayBook.Web.ViewModels.Account;
using DayBook.Web.ViewModels.Manage;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using System.Linq;
using System.Threading.Tasks;

namespace DayBook.Web.Controllers
{
    [Authorize(Roles = "Admin")]
    public class ManageController : Controller
    {
        private readonly IRepository<Invite> _inviteRepository;
        private readonly IAccountService _accountService;
        private readonly IEmailSender _emailSender;

        public ManageController(IRepository<Invite> inviteRepository,
            IAccountService accountService,
            IEmailSender emailSender)
        {
            _inviteRepository = inviteRepository;
            _accountService = accountService;
            _emailSender = emailSender;
        }

        [HttpGet]
        public IActionResult Index()
        {
            var users = _accountService.GetUsers();

            var list = (from user in users
                        select new UserViewModel
                        {
                            Id = user.Id,
                            UserName = user.UserName,
                            NickName = user.NickName,
                            Email = user.Email
                        }).ToList();

            return View(list);
        }

        // GET: /Manage/SendInvite
        [HttpGet]
        public ActionResult SendInvite() => View();

        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> SendInvite(InviteViewModel model)
        {
            if (!ModelState.IsValid)
            {
                return View(model);
            }

            var result = await _accountService.AddInviteAsync();

            if (!result.Success)
            {
                ModelState.AddModelError(string.Empty, result.Message);
                return View(model);
            }

            string msg = $"Invite link: { Url.Action("Register", "Account", new { result.Resource.Code }, "http")} ";

            //await _emailSender.SendEmailAsync("admin@gmail.com",model.Email, "DayBook invite", msg);

            return RedirectToAction("SendInvite", "Manage");
        }

        // POST: Manage/Delete/XXX
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteUser(string id)
        {
            if (id == null)
            {
                return BadRequest();
            }

            var result = await _accountService.DeleteAccountAsync(id);

            if (!result.Success)
            {
                ModelState.AddModelError(string.Empty, result.Message);
                return View();
            }

            return RedirectToAction(nameof(Index));
        }
    }
}