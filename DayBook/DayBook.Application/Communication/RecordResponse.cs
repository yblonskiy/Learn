using System;
using System.Collections.Generic;
using System.Text;
using ApplicationCore.Entities;

namespace DayBook.Application.Communication
{
    public class RecordResponse : BaseResponse<Record>
    {
        public RecordResponse(Record record) : base(record) { }

        public RecordResponse(string message) : base(message) { }
    }
}
