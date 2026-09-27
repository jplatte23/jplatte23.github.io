# Bookstore Code Samples

A Unity C# gameplay programming sample from a first-person bookstore project, focused on seamless portal traversal and collectible-driven floor progression.

## Gameplay Overview

The project used a first-person exploration structure where the player moves through bookstore floors, collects required items, unlocks or transitions to new floor states, and encounters a pursuing statue enemy. The strongest technical feature is the escalator/portal loop: the player crosses a trigger, is repositioned relative to a paired portal, and sees a matching portal view through a runtime render texture.

The progression system was originally implemented as separate item-specific manager/counter pairs. For this portfolio version, that logic has been lightly refactored into reusable components: one script handles collectable interaction, one tracks an objective's count and UI, and one applies the completion side effects such as loading a floor, disabling a portal, or toggling lights.

The goal of this sample is to show gameplay engineering decisions: separating object interaction from objective state, preserving relative transforms across portals, and coordinating gameplay state across enemy AI, death UI, respawn, scenes, and player progress.

## Gameplay Demo

![Escalator Portal Loop](Media/bookstore-looping.gif)

![Collectible Progression](Media/bookstore-objective-state.gif)

## My Role

This sample represents gameplay scripting work for portal traversal, collectible progression, floor state changes, lighting/portal activation, enemy behavior, and death/respawn flow. This portfolio folder includes light cleanup and organization to make the systems easier for another engineer to review.

## Architecture

```text
BookstoreCodeSamples/
├── PortalSystem/
│   ├── PortalTeleporter.cs
│   ├── MirrorPlayer.cs
│   ├── PortalTextureSetup.cs
│   └── PortalPlaneMatcher.cs
├── ProgressionSystem/
│   ├── Collectibles/
│   │   ├── CollectibleItem.cs
│   │   └── CollectibleObjective.cs
│   ├── FloorTriggers/
│   │   └── FloorEntranceTrigger.cs
│   └── ObjectiveCompletionAction.cs
```

### Portal System

`PortalTeleporter` handles the actual gameplay transition. It waits until the player overlaps the portal trigger, checks whether the player crossed from the valid side using a dot product, computes the rotation difference between the portal and receiver, then places the player at the matching receiver-relative offset.

`MirrorPlayer` handles the visual side of the illusion. It converts the player camera into the source portal's local space, transforms that position through the destination portal, and applies the portal rotation delta to the mirrored object.

`PortalTextureSetup` creates a runtime `RenderTexture` for the portal camera and assigns it to the portal material. `PortalPlaneMatcher` supports scene setup by matching one portal plane's relative position to the opposite escalator.

### Progression System

`CollectibleItem` owns the per-object interaction: player trigger overlap, mouse-over range checks, input, and disabling the collected object.

`CollectibleObjective` owns the objective state: current count, required count, UI text, collection events, completion events, and optional telemetry sampling.

`ObjectiveCompletionAction` owns the side effects that happen when an objective completes: additive scene loading, scene unloading, disabling collected item containers, toggling portals, and enabling/disabling lights.

`FloorEntranceTrigger` activates the correct objective when the player enters a floor, sets respawn state, updates the current level, toggles portals/lights, and optionally records the time-to-enter metric.

## Technical Highlights

- Transform-space portal teleportation with relative offset preservation.
- Directional portal gating with `Vector3.Dot`.
- Runtime render texture creation for portal camera display.
- Mirrored player/camera representation using local-space transform conversion.
- Reusable collectible interaction component.
- Objective-based collectible progress tracking.
- UnityEvents for collection and completion side effects.
- Additive scene loading and scene unloading for floor progression.
- Telemetry hooks behind `USE_USCG_TELEMETRY`.

## Design Decisions

The portal system is kept separate from progression and enemy logic because it solves a spatial transformation problem. The teleporter should only decide when and how to move the player through a portal; it should not know about floor objectives or collectibles.

The progression system is split into interaction, objective state, and completion actions. This directly addresses the original duplication where each collectible type had its own manager and counter pair. The refactor keeps the same gameplay behavior but makes the system easier to extend: tapes, sacks, limbs, books, or future objectives can share the same pickup and count logic while configuring different completion actions.

Completion side effects are intentionally data-driven through serialized references. Scene names still exist because Unity scene loading requires string or asset references, but the logic no longer needs a separate hard-coded script for every collectible type.

The enemy/respawn system uses a small `GameStateTracker` so AI and UI are not comparing against raw string states such as `"ingame"` or `"deathUI"`. This is a light cleanup, not a full framework rewrite.

Telemetry is optional in this sample. The original project recorded collectible positions, death positions, and level timing. In this portfolio version, those hooks are preserved behind a compile flag so the code can be read without requiring the original telemetry package.

## Interesting Engineering Challenges

- Preserving player-relative position and facing direction through paired portal transforms.
- Making a portal transition feel seamless by separating gameplay teleportation from mirror/camera presentation.
- Reducing duplicated collectible scripts into reusable interaction and objective components.
- Coordinating one objective completion with multiple world side effects: scene loading, portal state, item containers, lighting, audio, and telemetry.

## Skills Demonstrated

- Unity C#
- Gameplay programming
- Transform math
- Camera/render texture systems
- Trigger-based interaction
- UI objective tracking
- Scene management
- Event-driven gameplay logic
- Code refactoring for portfolio presentation
- Inspector-driven configuration

## Future Improvements

- Replace scene-name strings with addressable scene references or a ScriptableObject floor definition.
- Create ScriptableObject data for objective labels, required counts, scene transitions, and sounds.