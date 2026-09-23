import asyncio
import edge_tts

async def main():
    tts = edge_tts.Communicate("tes suara", voice="en-US-JennyNeural")
    await tts.save("out.mp3")

asyncio.run(main())
print("SELESAI")
