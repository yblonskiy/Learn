using DayBook.Application.Interfaces;
using DayBook.Web.ViewModels.Account;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Caching.Distributed;
using System.Threading.Tasks;

namespace DayBook.Web.Controllers.Api
{
    [Route("api/[controller]")]
    [ApiController]
    public class AuthController : ControllerBase
    {
        private readonly IAccountService _accountService;

        public AuthController(IAccountService accountService)
        {
            _accountService = accountService;
        }

        // POST api/auth/login
        [HttpPost("login")]
        public async Task<IActionResult> Login([FromBody]LoginViewModel credentials)
        {
            if (!ModelState.IsValid)
            {
                return BadRequest(ModelState);
            }

            var user = await _accountService.GetVerifiedUserAsync(credentials.Email, credentials.Password);

            if (user == null)
            {
                return NotFound();
            }

            var token = _accountService.GenerateToken(user.Id);

            HttpContext.Session.SetString("JWToken", token);

            return new OkObjectResult(token);
        }

    }
}