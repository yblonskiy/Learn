using System.ComponentModel.DataAnnotations;

namespace DayBook.Web.ViewModels.Manage
{
    public class InviteViewModel
    {
        [Required, MaxLength(256)]
        [DataType(DataType.EmailAddress)]
        [Display(Name = "Email")]
        [EmailAddress]
        public string Email { get; set; }

        public string StatusMessage { get; set; }
    }
}
