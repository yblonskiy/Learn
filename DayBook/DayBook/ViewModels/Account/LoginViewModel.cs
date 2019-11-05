using System.ComponentModel.DataAnnotations;

namespace DayBook.Web.ViewModels.Account
{
    public class LoginViewModel
    {
        [Required, MaxLength(256)]
        [DataType(DataType.EmailAddress)]
        [Display(Name = "Email")]
        [EmailAddress]
        public string Email { get; set; }

        [Required]
        [DataType(DataType.Password)]
        [Display(Name = "Password")]
        public string Password { get; set; }

        public string ReturnUrl { get; set; }
    }
}
