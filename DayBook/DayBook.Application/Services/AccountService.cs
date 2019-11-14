using ApplicationCore.Entities;
using ApplicationCore.Interfaces;
using DayBook.Application.Auth;
using DayBook.Application.Communication;
using DayBook.Application.Interfaces;
using Infrastructure.Identity;
using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.Options;
using Microsoft.IdentityModel.Tokens;
using System;
using System.IdentityModel.Tokens.Jwt;
using System.Linq;
using System.Security.Claims;
using System.Text;
using System.Threading.Tasks;
using DayBook.Application.Helpers;

namespace DayBook.Application.Services
{
    public class AccountService : IAccountService
    {
        private readonly UserManager<ApplicationUser> _userManager;
        private readonly SignInManager<ApplicationUser> _signInManager;
        private RoleManager<IdentityRole> _roleManager;

        private readonly IRepository<Invite> _inviteRepository;
        private readonly IRepository<ApplicationUser> _userRepository;
        private readonly IRepository<Record> _recordRepository;

        private readonly JwtIssuerOptions _jwtOptions;

        public AccountService(UserManager<ApplicationUser> userManager,
            SignInManager<ApplicationUser> signInManager,
            RoleManager<IdentityRole> roleManager,
            IRepository<Invite> inviteRepository,
            IRepository<ApplicationUser> userRepository,
            IRepository<Record> recordRepository,
            IOptions<JwtIssuerOptions> jwtOptions)
        {
            _userManager = userManager;
            _signInManager = signInManager;
            _roleManager = roleManager;

            _inviteRepository = inviteRepository;
            _recordRepository = recordRepository;
            _userRepository = userRepository;

            _jwtOptions = jwtOptions.Value;
        }

        /// <summary>
        /// Register a new user
        /// </summary>
        /// <returns></returns>
        public async Task<UserResponse> RegisterUserAsync(ApplicationUser user, string password, string code)
        {
            Invite invite = await _inviteRepository.GetSingleAsync(i => i.Code == code);

            if (invite == null)
            {
                return new UserResponse("Invite not found.");
            }

            try
            {
                var result = await _userManager.CreateAsync(user, password);

                if (!result.Succeeded)
                {
                    return new UserResponse("An error occurred when creating a new user.");
                }

                await _inviteRepository.DeleteAsync(invite);

                var userAccount = await _userManager.FindByEmailAsync(user.Email);

                await _userManager.AddToRoleAsync(userAccount, "User");
                await _signInManager.SignInAsync(userAccount, false);

                return new UserResponse(userAccount);
            }
            catch (Exception ex)
            {
                return new UserResponse($"An error occurred when registering the user: {ex.Message}");
            }

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
            catch
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
                return false;
            }
        }

        /// <summary>
        /// Delete account
        /// </summary>
        /// <returns></returns>
        public async Task<UserResponse> MarkAsDeletedAsync(string userName)
        {
            var user = await _userManager.FindByNameAsync(userName);

            if (user == null)
            {
                return new UserResponse("User not found.");
            }

            user.DateDeleted = DateTime.Now;

            try
            {
                await _userManager.UpdateAsync(user);

                return new UserResponse(user);
            }
            catch (Exception ex)
            {
                return new UserResponse($"An error occurred when marking to delete the account: {ex.Message}");
            }
        }

        /// <summary>
        /// Delete account
        /// </summary>
        /// <returns></returns>
        public async Task<UserResponse> DeleteAccountAsync(string id)
        {
            var user = await _userManager.FindByIdAsync(id);

            if (user == null)
            {
                return new UserResponse("User not found.");
            }

            try
            {
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

                return new UserResponse(user);
            }
            catch (Exception ex)
            {
                return new UserResponse($"An error occurred when deleting the account: {ex.Message}");
            }
        }

        /// <summary>
        /// Check that user can re-open account
        /// </summary>
        /// <returns></returns>
        public async Task<ApplicationUser> GetReopenUserAsync(string email)
        {
            try
            {
                var user = await _userManager.FindByEmailAsync(email);

                if (user != null)
                {
                    if (user.DateDeleted != null && user.DateDeleted >= DateTime.Now.AddDays(-2))
                    {
                        return user;
                    }
                }
            }
            catch
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
        public async Task<ApplicationUser> GetUserByIdAsync(string userId)
        {
            return await _userManager.FindByIdAsync(userId);
        }

        public async Task<ApplicationUser> GetVerifiedUserAsync(string email, string password)
        {
            var user = await _userManager.FindByEmailAsync(email);

            if (user == null)
                return null;

            if (await _userManager.CheckPasswordAsync(user, password))
            {
                return user;
            }

            return null;
        }

        /// <summary>
        /// Re-open account
        /// </summary>
        /// <returns></returns>
        public async Task<UserResponse> ReopenAccountAsync(string email)
        {
            var user = await GetReopenUserAsync(email);

            if (user == null)
            {
                return new UserResponse("User not found.");
            }

            user.DateDeleted = null;

            try
            {
                await _userManager.UpdateAsync(user);

                return new UserResponse(user);
            }
            catch (Exception ex)
            {
                return new UserResponse($"An error occurred when reopening the account: {ex.Message}");
            }
        }

        public async Task<InviteResponse> AddInviteAsync()
        {
            var invite = new Invite()
            {
                DateCreated = DateTime.Now
            };

            try
            {
                await _inviteRepository.AddAsync(invite);

                return new InviteResponse(invite);
            }
            catch (Exception ex)
            {
                return new InviteResponse($"An error occurred when adding a invite: {ex.Message}");
            }
        }

        /// <summary>
        /// Logout
        /// </summary>
        /// <returns></returns>
        public async Task LogoutUserAsync()
        {
            await _signInManager.SignOutAsync();
        }

        public async Task<bool> IsExistInviteAsync(string code)
        {
            return await _inviteRepository.GetSingleAsync(i => i.Code == code) != null;
        }

        public async Task<string> GenerateToken(ApplicationUser user)
        {
            Claim[] claims = new[]
                {
                    new Claim(ClaimTypes.Name, user.Id.ToString()),
                    new Claim(ClaimTypes.Email, user.Email)
            };

            ClaimsIdentity claimsIdentity = new ClaimsIdentity(claims, "Token");

            var roles = await _userManager.GetRolesAsync(user);

            claimsIdentity.AddClaims(roles.Select(r => new Claim(ClaimTypes.Role, r)));

            var JWToken = new JwtSecurityToken(
                issuer: _jwtOptions.Audience,
                audience: _jwtOptions.Audience,
                claims: claimsIdentity.Claims,
                notBefore: new DateTimeOffset(DateTime.Now).DateTime,
                expires: new DateTimeOffset(DateTime.Now.AddDays(1)).DateTime,
                signingCredentials: new SigningCredentials(new SymmetricSecurityKey(Encoding.ASCII.GetBytes(_jwtOptions.SecretKey)), SecurityAlgorithms.HmacSha256Signature)
            );

            var token = new JwtSecurityTokenHandler().WriteToken(JWToken);

            return token;
        }
    }
}
