using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using System.ComponentModel.DataAnnotations;

namespace DayBook.Web.ViewModels.Record
{
    public class RecordViewModel
    {        
        public string Id { get; set; }

        [Required, MaxLength(100)]
        public string Title { get; set; }
                
        public DateTime DateCreated { get; set; }

        [Required]
        public string Body { get; set; }
    }
}
