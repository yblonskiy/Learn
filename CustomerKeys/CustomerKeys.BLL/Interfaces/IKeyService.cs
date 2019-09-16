using CustomerKeys.BLL.DTO;
using System.Collections.Generic;

namespace CustomerKeys.BLL.Interfaces
{
    public interface IKeyService
    {
        bool GenerateKey(CustomerDTO keyRequestDTO, int keyNumbers);

        IEnumerable<CustomerDTO> GetCustomers();

        bool IsExistsKey(string key);

        IEnumerable<KeyDTO> GetKeys(int customerId);

        void Dispose();
    }
}
