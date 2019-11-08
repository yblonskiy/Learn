using System;
using System.Collections.Generic;
using System.Text;
using ApplicationCore.Entities;
using Infrastructure.Identity;

namespace DayBook.Application.Communication
{
    public class UserResponse : BaseResponse<ApplicationUser>
    {
        public UserResponse(ApplicationUser user) : base(user) { }

        public UserResponse(string message) : base(message) { }
    }
}
