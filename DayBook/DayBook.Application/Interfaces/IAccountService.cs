using ApplicationCore.Entities;
using System.Collections.Generic;
using System.Threading.Tasks;
using Infrastructure.Identity;
using System.Linq;

namespace DayBook.Application.Interfaces
{
    public interface IAccountService
    {
        Task<bool> RegisterUserAsync(ApplicationUser user, string password, string code);

        Task<bool> LoginUserAsync(string email, string password);

        Task LogoutUserAsync();

        Task<bool> DeleteAccountAsync(string id);

        Task<bool> IsMarkedAsDeletedAsync(string userName);

        Task<bool> MarkAsDeletedAsync(string userName);

        Task<ApplicationUser> IsPossibleReopenAccountAsync(string email);

        Task<bool> ReopenAccountAsync(string email);

        IQueryable<ApplicationUser> GetUsers();

        Task<ApplicationUser> GetUserAsync(string userName);
    }
}
