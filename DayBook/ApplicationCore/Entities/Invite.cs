using System;
using System.Linq;
using System.ComponentModel.DataAnnotations;

namespace ApplicationCore.Entities
{
    public class Invite
    {
        [Required]
        public int Id { get; set; }

        [Required]
        [StringLength(15)]
        public string Code { get; private set; }               

        [Required]
        public DateTime DateCreated { get; set; }
        
        public Invite()
        {
            this.Code = RandomString(15);
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
