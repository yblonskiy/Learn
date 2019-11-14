using Microsoft.AspNetCore.Identity;
using System.Collections.Generic;
using System;

namespace Infrastructure.Identity
{
    public class ApplicationUser : IdentityUser
    {
        public string NickName { get; set; }

        public DateTime? DateDeleted { get; set; }
    }
}
