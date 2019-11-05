using DayBook.Application.Interfaces;
using System.Net.Mail;
using System.Net;
using System.Threading.Tasks;

namespace DayBook.Application.Services
{
    public class EmailSender : IEmailSender
    {
        public async Task SendEmailAsync(string from, string to, string subject, string body)
        {
            var smtpClient = new SmtpClient
            {
                Host = "smtp.gmail.com", // set your SMTP server name here
                Port = 587, // Port 
                EnableSsl = true,
                Credentials = new NetworkCredential(from, "password")
            };

            using (var message = new MailMessage(from, to)
            {
                Subject = subject,
                Body = body
            })
            {
                await smtpClient.SendMailAsync(message);
            }
        }
    }
}
