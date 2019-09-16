using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CustomerKeys.BLL.DTO
{
    public class KeyDTO
    {
        public int Id { get; set; }

        public string Value { get; set; }

        public int CustomerId { get; set; }
    }
}
