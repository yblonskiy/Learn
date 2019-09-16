using CustomerKeys.BLL.DTO;
namespace CustomerKeys.WEB.Models
{
    public class KeyRequestModel
    {
        public CustomerDTO Customer { get; set; }

        public ushort Numbers { get; set; }
    }
}