<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0f172a;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #cbd5e1;
            line-height: 1.6;
            -webkit-text-size-adjust: 100%;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #1e293b;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #334155;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .header {
            background: linear-gradient(135deg, #7b68ee 0%, #6366f1 100%);
            padding: 28px 32px;
            text-align: left;
        }
        .header-logo {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #ffffff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .header-logo span {
            color: #fcd34d;
        }
        .content {
            padding: 32px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #f8fafc;
            margin-bottom: 12px;
        }
        .actor-badge {
            display: inline-flex;
            align-items: center;
            background-color: #334155;
            border-radius: 9999px;
            padding: 4px 12px 4px 6px;
            font-size: 13px;
            font-weight: 500;
            color: #e2e8f0;
            margin-bottom: 16px;
        }
        .actor-avatar {
            width: 22px;
            height: 22px;
            border-radius: 9999px;
            background: #7b68ee;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            margin-right: 8px;
            text-transform: uppercase;
        }
        .message-box {
            font-size: 15px;
            color: #cbd5e1;
            margin-bottom: 24px;
        }
        .task-card {
            background-color: #0f172a;
            border: 1px solid #334155;
            border-left: 4px solid #7b68ee;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 24px;
        }
        .task-title {
            font-size: 16px;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 8px;
        }
        .meta-tags {
            font-size: 12px;
            color: #94a3b8;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }
        .meta-tag {
            display: inline-block;
            background-color: #1e293b;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #334155;
        }
        .changes-box {
            background-color: #0f172a;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            border: 1px solid #334155;
        }
        .changes-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .change-row {
            display: flex;
            align-items: center;
            font-size: 14px;
            margin-top: 4px;
        }
        .old-val {
            color: #ef4444;
            text-decoration: line-through;
            background-color: rgba(239, 68, 68, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
        }
        .arrow {
            margin: 0 8px;
            color: #94a3b8;
        }
        .new-val {
            color: #10b981;
            font-weight: 600;
            background-color: rgba(16, 185, 129, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
        }
        .comment-quote {
            background-color: #0f172a;
            border-left: 3px solid #6366f1;
            border-radius: 0 8px 8px 0;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-style: italic;
            color: #e2e8f0;
            font-size: 14px;
        }
        .cta-container {
            text-align: center;
            margin: 28px 0 16px 0;
        }
        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #7b68ee 0%, #6366f1 100%);
            color: #ffffff !important;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(123, 104, 238, 0.35);
        }
        .footer {
            border-top: 1px solid #334155;
            padding: 20px 32px;
            background-color: #182234;
            font-size: 12px;
            color: #64748b;
            text-align: center;
        }
        .footer a {
            color: #818cf8;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <a href="{{ url('/') }}" class="header-logo">
                TaskManager<span>.</span>
            </a>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">Hello {{ $recipient->name }},</div>

            @if($actor)
                <div class="actor-badge">
                    <span class="actor-avatar">{{ strtoupper(substr($actor->name, 0, 1)) }}</span>
                    <span><strong>{{ $actor->name }}</strong></span>
                </div>
            @endif

            <p class="message-box">{{ $messageText }}</p>

            <!-- Task Context Card -->
            <div class="task-card">
                <div class="task-title">{{ $task->title }}</div>
                <div class="meta-tags">
                    @if($workspace)
                        <span class="meta-tag">Workspace: <strong>{{ $workspace->name }}</strong></span>
                    @endif
                    @if($project)
                        <span class="meta-tag">Project: <strong>{{ $project->name }}</strong></span>
                    @endif
                    <span class="meta-tag">Status: <strong>{{ $task->status?->name ?? 'None' }}</strong></span>
                    <span class="meta-tag">Priority: <strong>{{ ucfirst($task->priority) }}</strong></span>
                </div>
            </div>

            <!-- Old vs New Values if available -->
            @if(!empty($details['old']) || !empty($details['new']))
                <div class="changes-box">
                    <div class="changes-title">{{ $details['field_name'] ?? 'Change Details' }}</div>
                    <div class="change-row">
                        @if(!empty($details['old']))
                            <span class="old-val">{{ $details['old'] }}</span>
                            <span class="arrow">&rarr;</span>
                        @endif
                        <span class="new-val">{{ $details['new'] ?? 'Updated' }}</span>
                    </div>
                </div>
            @endif

            <!-- Comment quote if comment event -->
            @if(!empty($details['comment_body']))
                <div class="comment-quote">
                    "{{ \Illuminate\Support\Str::limit($details['comment_body'], 280) }}"
                </div>
            @endif

            <!-- CTA Button -->
            <div class="cta-container">
                <a href="{{ $taskUrl }}" class="cta-btn">View Task &rarr;</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>You received this automated notification because you are a participant or assignee on this task in <strong>TaskManager</strong>.</p>
            <p>To manage your notification settings, visit your profile preferences in TaskManager.</p>
        </div>
    </div>
</body>
</html>
