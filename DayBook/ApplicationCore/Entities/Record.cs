using System;
using System.Linq;
using System.ComponentModel.DataAnnotations;

namespace ApplicationCore.Entities
{
    public class Record
    {
        [StringLength(10)]
        public string Id { get; private set; }

        [Required]
        [StringLength(450)]
        public string UserId { get; set; }

        [Required]
        [MaxLength(100)]
        public string Title { get; set; }

        [Required]
        public DateTime DateCreated { get; set; }

        [Required]
        [MaxLength(500)]
        public string Body { get; set; }

        public Record()
        {
            this.Id = RandomString(10);
        }

        private string RandomString(int length)
        {
            Random random = new Random();
            const string chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            return new string(Enumerable.Repeat(chars, length)
              .Select(s => s[random.Next(s.Length)]).ToArray());
        }

    }
}
