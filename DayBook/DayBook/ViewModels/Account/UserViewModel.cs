using System.ComponentModel.DataAnnotations;
using System.Collections.Generic;
using System;

namespace DayBook.Web.ViewModels.Account
{
    public class UserViewModel
    {
        public string Id { get; set; }

        [Display(Name = "User Name")]
        public string UserName { get; set; }

        [Required, MaxLength(256)]
        [EmailAddress]
        [Display(Name = "Email")]
        public string Email { get; set; }

        [Required, MaxLength(256)]
        [Display(Name = "Nick Name")]
        public string NickName { get; set; }

        public ICollection<string> Roles { get; set; }

        public DateTime? DateDeleted { get; set; }
    }
}
