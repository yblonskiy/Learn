using System.Threading.Tasks;

namespace DayBook.Application.Interfaces
{
    public interface IEmailSender
    {
        Task SendEmailAsync(string from, string to, string subject, string body);
    }
}
