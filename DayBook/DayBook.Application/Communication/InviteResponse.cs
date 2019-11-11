using System;
using System.Collections.Generic;
using System.Text;
using ApplicationCore.Entities;

namespace DayBook.Application.Communication
{
    public class InviteResponse : BaseResponse<Invite>
    {
        public InviteResponse(Invite invite) : base(invite) { }

        public InviteResponse(string message) : base(message) { }
    }
}
