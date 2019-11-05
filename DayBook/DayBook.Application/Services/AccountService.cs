using ApplicationCore.Entities;
using ApplicationCore.Interfaces;
using DayBook.Application.Interfaces;
using Infrastructure.Identity;
using Microsoft.AspNetCore.Identity;
using System;
using System.Linq;
using System.Threading.Tasks;

namespace DayBook.Application.Services
{
    public class AccountService : IAccountService
    {
        private readonly UserManager<ApplicationUser> _userManager;
        private readonly SignInManager<ApplicationUser> _signInManager;
        private RoleManager<ApplicationRole> _roleManager;

        private readonly IRepository<Invite> _inviteRepository;
        private readonly IRepository<ApplicationUser> _userRepository;
        private readonly IRepository<Record> _recordRepository;

        public AccountService(UserManager<ApplicationUser> userManager,
            SignInManager<ApplicationUser> signInManager,
            RoleManager<ApplicationRole> roleManager,
            IRepository<Invite> inviteRepository,
            IRepository<ApplicationUser> userRepository,
            IRepository<Record> recordRepository)
        {
            _userManager = userManager;
            _signInManager = signInManager;
            _roleManager = roleManager;

            _inviteRepository = inviteRepository;
            _recordRepository = recordRepository;
            _userRepository = userRepository;
        }

        /// <summary>
        /// Register a new user
        /// </summary>
        /// <returns></returns>
        public async Task<bool> RegisterUserAsync(ApplicationUser user, string password, string code)
        {
            try
            {
                Invite invite = await _inviteRepository.GetSingleAsync(i => i.Code == code);

                if (invite == null)
                {
                    return false;
                }

                var result = await _userManager.CreateAsync(user, password);

                if (result.Succeeded)
                {
                    await _inviteRepository.DeleteAsync(invite);

                    var userAccount = await _userManager.FindByEmailAsync(user.Email);

                    await _userManager.AddToRoleAsync(userAccount, "User");

                    await _signInManager.SignInAsync(userAccount, false);

                    return true;
                }
            }
            catch { }

            return false;
        }

        /// <summary>
        /// Login a user
        /// </summary>
        /// <returns></returns>
        public async Task<bool> LoginUserAsync(string email, string password)
        {
            try
            {
                var result = await _signInManager.PasswordSignInAsync(email, password, false, false);

                if (result.Succeeded)
                {
                    return true;
                }
            }
            catch (Exception ex)
            {

            }

            return false;
        }

        /// <summary>
        /// Check that user marked as deleted
        /// </summary>
        /// <returns></returns>
        public async Task<bool> IsMarkedAsDeletedAsync(string email)
        {
            try
            {
                var user = await _userManager.FindByEmailAsync(email);
                if (user == null)
                {
                    throw new ApplicationException("Unable to load user.");
                }

                if (user.DateDeleted == null)
                {
                    return false;
                }
                else if (user.DateDeleted != null && user.DateDeleted < DateTime.Now.AddDays(-2))
                {
                    //remove account
                    await DeleteAccountAsync(user.Id);
                    await LogoutUserAsync();

                    return false;
                }
                else
                {
                    return true;
                }
            }
            catch (Exception ex)
            {

            }

            return false;
        }

        /// <summary>
        /// Delete account
        /// </summary>
        /// <returns></returns>
        public async Task<bool> MarkAsDeletedAsync(string userName)
        {
            try
            {
                var user = await _userManager.FindByNameAsync(userName);
                if (user == null)
                {
                    throw new ApplicationException("Unable to load user.");
                }

                user.DateDeleted = DateTime.Now;

                await _userManager.UpdateAsync(user);

                return true;
            }
            catch (Exception ex)
            {

            }

            return false;
        }
    
        /// <summary>
        /// Delete account
        /// </summary>
        /// <returns></returns>
        public async Task<bool> DeleteAccountAsync(string id)
        {
            try
            {
                var user = await _userManager.FindByIdAsync(id);
                if (user == null)
                {
                    throw new ApplicationException("Unable to load user.");
                }

                // here must be transaction

                // delete records
                await _recordRepository.DeleteWhereAsync(r => r.UserId == user.Id);

                var rolesForUser = await _userManager.GetRolesAsync(user);

                foreach (var item in rolesForUser)
                {
                    // delete roles
                    var result = await _userManager.RemoveFromRoleAsync(user, item);
                }

                // delete user
                await _userManager.DeleteAsync(user);

                return true;
            }
            catch (Exception ex)
            {

            }

            return false;
        }

        /// <summary>
        /// Check that user can re-open account
        /// </summary>
        /// <returns></returns>
        public async Task<ApplicationUser> IsPossibleReopenAccountAsync(string email)
        {
            try
            {
                var user = await _userManager.FindByEmailAsync(email);
                if (user == null)
                {
                    throw new ApplicationException("Unable to load user.");
                }

                if (user.DateDeleted != null && user.DateDeleted >= DateTime.Now.AddDays(-2))
                {
                    return user;
                }
            }
            catch (Exception ex)
            {
            }

            return null;
        }

        /// <summary>
        /// Check that user can re-open account
        /// </summary>
        /// <returns></returns>
        public IQueryable<ApplicationUser> GetUsers()
        {
            return _userManager.Users;
        }

        /// <summary>
        /// Check that user can re-open account
        /// </summary>
        /// <returns></returns>
        public async Task<ApplicationUser> GetUserAsync(string userName)
        {
            return await _userManager.FindByNameAsync(userName);
        }

        /// <summary>
        /// Re-open account
        /// </summary>
        /// <returns></returns>
        public async Task<bool> ReopenAccountAsync(string email)
        {
            try
            {
                var user = await IsPossibleReopenAccountAsync(email);
                if (user == null)
                {
                    throw new ApplicationException("Unable to load user.");
                }

                user.DateDeleted = null;

                await _userManager.UpdateAsync(user);



                return true;
            }
            catch (Exception ex)
            {
            }

            return false;
        }

        /// <summary>
        /// Logout
        /// </summary>
        /// <returns></returns>
        public async Task LogoutUserAsync()
        {
            await _signInManager.SignOutAsync();
        }

    }
}
