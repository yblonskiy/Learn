using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using YSD.Domain.Core;

namespace YSD.Web.Models
{
    public class UserViewModel
    {
        public UserViewModel(User user)
        {
            this.FirstName = user.FirstName;
            this.LastName = user.LastName;
            this.Email = user.Email;
        }

        public string FirstName { get; }

        public string LastName { get; }

        public string Email { get; }
    }
}