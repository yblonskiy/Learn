using DayBook.Application.Auth;
using DayBook.Application.Interfaces;
using DayBook.Web.ViewModels.Account;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Caching.Distributed;
using Microsoft.Extensions.Options;
using System.Security.Claims;
using System.Threading.Tasks;
using Newtonsoft.Json;
using DayBook.Application.Helpers;

namespace DayBook.Web.Controllers.Api
{
    [Route("api/[controller]")]
    [ApiController]
    public class AuthController : ControllerBase
    {
        private readonly IAccountService _accountService;
        private readonly IJwtFactory _jwtFactory;
        private readonly JwtIssuerOptions _jwtOptions;

        public AuthController(IAccountService accountService, IJwtFactory jwtFactory, IOptions<JwtIssuerOptions> jwtOptions)
        {
            _accountService = accountService;
            _jwtFactory = jwtFactory;
            _jwtOptions = jwtOptions.Value;
        }

        // POST api/auth/login
        [HttpPost("login")]
        public async Task<IActionResult> Login([FromBody]LoginViewModel credentials)
        {
            if (!ModelState.IsValid)
            {
                return BadRequest(ModelState);
            }

            //var user = await _accountService.GetVerifiedUserAsync(credentials.Email, credentials.Password);

            //if (user == null)
            //{
            //    return NotFound();
            //}

            //var token = await _accountService.GenerateToken(user);

            //HttpContext.Session.SetString("JWToken", token);

            var identity = await GetClaimsIdentity(credentials.Email, credentials.Password);
            if (identity == null)
            {
                return BadRequest("Invalid username or password.");
            }

            var jwt = await Tokens.GenerateJwt(identity, _jwtFactory, credentials.Email, _jwtOptions, new JsonSerializerSettings { Formatting = Formatting.Indented });

            return new OkObjectResult(jwt);
        }

        private async Task<ClaimsIdentity> GetClaimsIdentity(string userName, string password)
        {
            if (string.IsNullOrEmpty(userName) || string.IsNullOrEmpty(password))
                return await Task.FromResult<ClaimsIdentity>(null);

            // get the user to verifty
            var userToVerify = await _accountService.GetVerifiedUserAsync(userName, password);

            if (userToVerify == null) return await Task.FromResult<ClaimsIdentity>(null);

            return await Task.FromResult(_jwtFactory.GenerateClaimsIdentity(userName, userToVerify.Id));
        }

    }
}