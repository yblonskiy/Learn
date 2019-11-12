using ApplicationCore.Entities;
using System.Collections.Generic;
using System.Threading.Tasks;
using Infrastructure.Identity;
using DayBook.Application.Communication;
using System.Linq;

namespace DayBook.Application.Interfaces
{
    public interface IAccountService
    {
        Task<UserResponse> RegisterUserAsync(ApplicationUser user, string password, string code);

        Task<bool> LoginUserAsync(string email, string password);

        Task LogoutUserAsync();

        Task<UserResponse> DeleteAccountAsync(string id);

        Task<bool> IsMarkedAsDeletedAsync(string userName);

        Task<UserResponse> MarkAsDeletedAsync(string userName);

        Task<ApplicationUser> GetReopenUserAsync(string email);

        Task<UserResponse> ReopenAccountAsync(string email);

        IQueryable<ApplicationUser> GetUsers();

        Task<ApplicationUser> GetUserByIdAsync(string userId);

        Task<ApplicationUser> GetVerifiedUserAsync(string userName, string password);

        Task<bool> IsExistInviteAsync(string code);

        Task<InviteResponse> AddInviteAsync();

        string GenerateToken(string userId);
    }
}
