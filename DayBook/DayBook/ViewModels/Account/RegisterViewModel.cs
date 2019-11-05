using System.ComponentModel.DataAnnotations;

namespace DayBook.Web.ViewModels.Account
{
    public class RegisterViewModel
    {
        [Required, MaxLength(256)]
        [EmailAddress]
        [Display(Name = "Email")]
        public string Email { get; set; }

        [Required, MaxLength(256)]
        [Display(Name = "Nick Name")]
        public string NickName { get; set; }

        [Required]
        [StringLength(100, ErrorMessage = "The {0} must be at least {2} characters long.", MinimumLength = 6)]
        [DataType(DataType.Password)]
        [Display(Name = "Password")]
        public string Password { get; set; }

        [DataType(DataType.Password)]
        [Display(Name = "Confirm password")]
        [Compare("Password", ErrorMessage = "The password and confirmation password do not match.")]
        public string ConfirmPassword { get; set; }

        [StringLength(15)]
        public string Code { get; set; }
    }
}
