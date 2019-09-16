using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CustomerKeys.BLL.DTO
{
    public class KeyRequestDTO
    {
        public CustomerDTO Customer { get; set; }

        public ushort Numbers { get; set; }
    }
}
