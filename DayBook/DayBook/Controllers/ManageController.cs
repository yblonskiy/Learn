using ApplicationCore.Entities;
using ApplicationCore.Interfaces;
using DayBook.Application.Interfaces;
using DayBook.Web.ViewModels.Account;
using DayBook.Web.ViewModels.Manage;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using System;
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

        public IActionResult Index()
        {
            var users = _accountService.GetUsers().Include(u => u.UserRoles).ThenInclude(ur => ur.Role).ToList();

            var list = (from user in users
                        select new UserViewModel
                        {
                            Id = user.Id,
                            UserName = user.UserName,
                            NickName = user.NickName,
                            Email = user.Email,
                            Roles = user.UserRoles.Select(i => i.Role.ToString()).ToList()
                        }).ToList();

            return View(list);
        }

        // GET: /Manage/SendInvite
        [HttpGet]
        public ActionResult SendInvite()
        {
            return View();
        }

        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> SendInvite(InviteViewModel model)
        {
            if (!ModelState.IsValid)
            {
                return BadRequest(ModelState);
            }

            var invite = new Invite()
            {
                DateCreated = DateTime.Now
            };

            try
            {
                await _inviteRepository.AddAsync(invite);

                string msg = $"Invite link: { Url.Action("Register", "Account", new { invite.Code }, "http")} ";

                //await _emailSender.SendEmailAsync("admin@gmail.com",model.Email, "DayBook invite", msg);

                model.StatusMessage = "Invite has been sent.";
            }
            catch (Exception ex)
            {
                ModelState.AddModelError("", "Unable to create invite changes. ");
                model.StatusMessage = "Invite has not been sent.";
            }

            return View(model);
        }

        // POST: Manage/Delete/XXX
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteUser(string id)
        {
            if (id == null)
            {
                return NotFound();
            }

            try
            {
                await _accountService.DeleteAccountAsync(id);
            }
            catch (Exception ex)
            {

            }

            return RedirectToAction(nameof(Index));
        }
    }
}