using System;
using System.ComponentModel.DataAnnotations;

namespace DayBook.Web.ViewModels.Record
{
    public class RecordViewModel
    {
        public string Id { get; set; }

        public string UserId { get; set; }

        [Required, MaxLength(100)]
        public string Title { get; set; }

        [Display(Name = "Date Created")]
        public DateTime DateCreated { get; set; }

        [Required]
        public string Body { get; set; }
    }
}
