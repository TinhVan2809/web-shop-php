- Dùng **git checkout -b [tên nhánh]** để tạo nhánh mới khi làm một chức năng hãy chỉnh sửa nào đó 
- Quan trọng: Sau khi push vào nhánh (nhánh vừa tạo) -> lên github tạo pull request xong đợi kiểm duyệt rồi mới megre vào main nhằm tránh bị conflig
- Sau khi megre thành công dùng 2 câu lệnh:
```bash
git branch -d feature/login        # Xóa local
git push origin --delete feature/login # Xóa remote
```
- Luôn ```git fetch``` để xem có gì thay đổi trên nhánh main không rồi ```git pull``` để lấy code mới nhất về