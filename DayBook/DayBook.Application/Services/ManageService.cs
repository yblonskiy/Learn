using ApplicationCore.Entities;
using ApplicationCore.Interfaces;
using DayBook.Application.Interfaces;
using System;
using System.Linq;
using System.Threading.Tasks;

namespace DayBook.Application.Services
{
    public class ManageService : IManageService
    {
        private readonly IAccountService _accountService;

        private readonly IRepository<Invite> _inviteRepository;

        public ManageService(IRepository<Invite> inviteRepository,
            IAccountService accountService)
        {
            _inviteRepository = inviteRepository;

            _accountService = accountService;
        }

        /// <summary>
        /// Remove old invites
        /// Remove accounts which marked as deleted
        /// </summary>
        /// <returns></returns>
        public async Task ClearUnusedAsync()
        {
            await RemoveOldInvitesAsync();
            await RemoveDeletedAccountsAsync();
        }

        /// <summary>
        /// Remove old (older 2 days) invites
        /// </summary>
        /// <returns></returns>
        public async Task RemoveOldInvitesAsync()
        {
            await _inviteRepository.DeleteWhereAsync(i => i.DateCreated < DateTime.Now.AddDays(-2));
        }

        /// <summary>
        /// Remove accounts marked as deleted
        /// </summary>
        /// <returns></returns>
        public async Task RemoveDeletedAccountsAsync()
        {
            var users = _accountService.GetUsers().Where(u => u.DateDeleted != null && u.DateDeleted < DateTime.Now.AddDays(-2)).ToList();

            foreach (var user in users)
            {
                await _accountService.DeleteAccountAsync(user.Id);
            }
        }
    }
}
