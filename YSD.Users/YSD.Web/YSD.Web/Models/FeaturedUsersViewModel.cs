using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace YSD.Web.Models
{
    public class FeaturedUsersViewModel
    {
        public FeaturedUsersViewModel(IEnumerable<UserViewModel> users)
        {
            this.Users = users;
        }

        public IEnumerable<UserViewModel> Users { get; }
    }
}