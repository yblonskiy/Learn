using ApplicationCore.Entities;
using System.Collections.Generic;
using System.Threading.Tasks;
using Infrastructure.Identity;

namespace DayBook.Application.Interfaces
{
    public interface IManageService
    {
        Task ClearUnusedAsync();

        Task RemoveOldInvitesAsync();

        Task RemoveDeletedAccountsAsync();
    }
}
